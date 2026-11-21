<?php

namespace Database\Seeders;

use App\Models\User;
use Carbon\CarbonImmutable;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AttendanceSeeder extends Seeder
{
    public function run(): void
    {
        $users = User::orderBy('id')->get();

        if ($users->isEmpty()) {
            return;
        }

        DB::transaction(function () use ($users): void {
            $this->clearExistingAttendance($users->pluck('id')->all());

            $now = CarbonImmutable::now();

            foreach ($users as $user) {
                if ($user->email === 'user1@example.com') {
                    $this->seedUser1($user->id, $now);
                } else {
                    $this->seedOtherUser($user->id, $now);
                }
            }
        });
    }

    // Seeder再実行時の重複を避けるため、対象ユーザーの勤怠・休憩を削除する。
    private function clearExistingAttendance(array $userIds): void
    {
        $attendanceIds = DB::table('attendances')
            ->whereIn('user_id', $userIds)
            ->pluck('id');

        DB::table('break_records')
            ->whereIn('attendance_id', $attendanceIds)
            ->delete();

        DB::table('attendances')
            ->whereIn('user_id', $userIds)
            ->delete();
    }

    /**
     * user1：過去5か月：各月の平日15日、当月は17日分
     */
    private function seedUser1(int $userId, CarbonImmutable $now): void
    {
        for ($monthsAgo = 5; $monthsAgo >= 1; $monthsAgo--) {
            $month = $now->subMonthsNoOverflow($monthsAgo);

            foreach ($this->weekdaysInMonth($month, 15) as $date) {
                $this->createAttendance(
                    $userId,
                    $date,
                    '09:00:00',
                    '18:00:00'
                );
            }
        }

        $patterns = [
            ...array_fill(0, 10, ['09:00:00', '18:00:00']), // 通常10日
            ...array_fill(0, 3, ['09:00:00', '20:00:00']),  // 残業3日
            ...array_fill(0, 2, ['09:30:00', '18:00:00']),  // 遅刻2日
            ['09:00:00', '17:00:00'],                       // 早退1日
            ['08:00:00', '21:00:00'],                       // 長時間労働1日
        ];

        $currentMonthDates = $this->weekdaysInMonth($now, count($patterns));

        foreach ($patterns as $index => [$clockIn, $clockOut]) {
            $this->createAttendance(
                $userId,
                $currentMonthDates[$index],
                $clockIn,
                $clockOut
            );
        }
    }

    //他ユーザー
    private function seedOtherUser(
        int $userId,
        CarbonImmutable $now
    ): void {
        for ($monthsAgo = 5; $monthsAgo >= 1; $monthsAgo--) {
            $month = $now->subMonthsNoOverflow($monthsAgo);

            $this->seedDeterministicMonth(
                $userId,
                $this->weekdaysInMonth($month),
                $monthsAgo
            );
        }

        $currentMonthDates = array_values(array_filter(
            $this->weekdaysInMonth($now),
            fn(CarbonImmutable $date): bool => $date->lte($now)
        ));

        $this->seedDeterministicMonth(
            $userId,
            $currentMonthDates,
            0
        );
    }

    // 日付順の番号に基づいて、毎回同じ勤務データを作成する。
    private function seedDeterministicMonth(
        int $userId,
        array $dates,
        int $monthOffset
    ): void {
        foreach ($dates as $index => $date) {
            // 平日4日中3日を出勤日にする（毎回同じ日付が選ばれる）
            if (($index + $monthOffset) % 4 === 0) {
                continue;
            }

            // 出勤日のパターンも日付順で固定
            [$clockIn, $clockOut] = match (($index + $monthOffset) % 4) {
                0 => ['09:00:00', '18:00:00'],
                1 => ['08:45:00', '18:00:00'],
                2 => ['09:00:00', '19:30:00'],
                default => ['09:30:00', '18:00:00'],
            };

            $this->createAttendance(
                $userId,
                $date,
                $clockIn,
                $clockOut
            );
        }
    }

    /**
     * 指定月の平日を取得する。
     * $limit を指定した場合は先頭からその件数だけ返す。
     *
     * @return CarbonImmutable[]
     */
    private function weekdaysInMonth(
        CarbonImmutable $month,
        ?int $limit = null
    ): array {
        $startOfMonth = $month->startOfMonth();
        $endOfMonth = $month->endOfMonth();

        $dates = [];
        for ($date = $startOfMonth; $date->lte($endOfMonth); $date = $date->addDay()) {
            if (in_array($date->dayOfWeek, [CarbonImmutable::SATURDAY, CarbonImmutable::SUNDAY], true)) {
                continue;
            }
            $dates[] = $date;
            if ($limit !== null && count($dates) >= $limit) {
                break;
            }
        }

        return $dates;
    }

    /**
     * 勤怠と12:00〜13:00の休憩を作成する。
     */
    private function createAttendance(
        int $userId,
        CarbonImmutable $date,
        string $clockIn,
        string $clockOut
    ): void {
        $attendanceDate = $date->toDateString();
        $timestamp = now();

        $attendanceId = DB::table('attendances')->insertGetId([
            'user_id' => $userId,
            'attendance_date' => $attendanceDate,
            'clock_in' => "{$attendanceDate} {$clockIn}",
            'clock_out' => "{$attendanceDate} {$clockOut}",
            'status' => 'off',
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);

        DB::table('break_records')->insert([
            'attendance_id' => $attendanceId,
            'break_in' => "{$attendanceDate} 12:00:00",
            'break_out' => "{$attendanceDate} 13:00:00",
            'created_at' => $timestamp,
            'updated_at' => $timestamp,
        ]);
    }
}
