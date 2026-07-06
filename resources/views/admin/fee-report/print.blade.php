<!DOCTYPE html>
<html lang="bn">
<head>
    <meta charset="UTF-8">
    <title>{{ $reportTitle ?? 'ফি রিপোর্ট' }}</title>
</head>
<body>

    @include('admin.fee-report.partials.report')

    <script>
        window.onload = function () {
            window.print();
        };
    </script>

</body>
</html>