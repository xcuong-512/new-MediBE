<!DOCTYPE html>
<html>
<head>
    <meta charset="UTF-8">
    <title>Xác nhận đặt lịch</title>
</head>
<body style="font-family: Arial, sans-serif; line-height: 1.6;">
    <h2>✅ Đặt lịch khám thành công</h2>

    <p>Xin chào <b>{{ $patient_name }}</b>,</p>
    <p>Bạn đã đặt lịch khám thành công trên <b>MediConnect</b>.</p>

    <h3>Thông tin lịch khám</h3>
    <ul>
        <li><b>Mã lịch khám:</b> {{ $appointment_code }}</li>
        <li><b>Bác sĩ:</b> {{ $doctor_name }}</li>
        <li><b>Ngày:</b> {{ $date }}</li>
        <li><b>Giờ:</b> {{ $start_time }} - {{ $end_time }}</li>
        <li><b>Hình thức:</b> {{ strtoupper($type) }}</li>
        <li><b>Trạng thái:</b> {{ strtoupper($status) }}</li>
    </ul>

    <p><b>Trân trọng,</b><br>MediConnect Team</p>
</body>
</html>
