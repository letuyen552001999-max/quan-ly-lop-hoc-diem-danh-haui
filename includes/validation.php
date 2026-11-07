<?php
/** Các quy tắc kiểm tra dữ liệu dùng chung, trả về mảng thông báo lỗi. */
function text_length(string $v): int {
    return function_exists('mb_strlen') ? mb_strlen($v,'UTF-8') : preg_match_all('/./us',$v);
}
function check_code(string $value, string $label): array {
    return preg_match('/^[A-Z0-9_-]{2,20}$/D', $value) ? [] : [$label . ' gồm 2–20 ký tự A–Z, số, _ hoặc -.'];
}
function check_text(string $value, string $label, int $limit=100): array {
    return trim($value)==='' || text_length($value)>$limit ? [$label.' bắt buộc, không quá '.$limit.' ký tự.'] : [];
}
function check_weekday(int $weekday): bool { return $weekday>=2 && $weekday<=8; }
function check_hours(string $start, string $end): bool {
    return (bool)preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D',$start)
        && (bool)preg_match('/^(?:[01][0-9]|2[0-3]):[0-5][0-9]$/D',$end)
        && $end>$start;
}
function check_date(string $date): bool {
    $d=DateTimeImmutable::createFromFormat('!Y-m-d',$date);
    return $d!==false && $d->format('Y-m-d')===$date;
}
function attendance_ratio(array $counts): float {
    $total=array_sum($counts);
    return $total>0 ? round(100*((int)($counts['Có mặt']??0)+(int)($counts['Đi muộn']??0))/$total,1) : 0.0;
}

/** Chính sách mật khẩu dùng chung cho tài khoản mẫu và người dùng. */
function password_errors(string $password): array {
    $bytes = strlen($password);
    if ($bytes < 10) return ['Mật khẩu cần có ít nhất 10 ký tự.'];
    if ($bytes > 72) return ['Mật khẩu vượt quá 72 byte, hãy chọn mật khẩu ngắn hơn.'];
    if (trim($password) === '') return ['Mật khẩu không được chỉ chứa khoảng trắng.'];
    return [];
}
