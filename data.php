<?php
header('Content-Type: application/json');

$serverName = "192.168.3.14,55495";
$connectionOptions = [
    "Database" => "ILM_SCS_DATA",
    "Uid" => "sa",
    "PWD" => "ILM4Corporation",
    "Encrypt" => true,
    "TrustServerCertificate" => true
];

$conn = sqlsrv_connect($serverName, $connectionOptions);

if (!$conn) {
    http_response_code(500);
    echo json_encode(["error" => "Database connection failed"]);
    exit;
}

$sql = "
SELECT 
    CONVERT(date, DATEADD(WEEK, DATEDIFF(WEEK, 0, DateCreated), 0)) AS WeekStart,
    SUM(DocCnt) AS TotalDocCnt
FROM dbo.batchtable
WHERE DateCreated >= DATEADD(WEEK, -4, DATEADD(WEEK, DATEDIFF(WEEK, 0, GETDATE()), 0))
  AND DateCreated < GETDATE()
GROUP BY DATEADD(WEEK, DATEDIFF(WEEK, 0, DateCreated), 0)
ORDER BY WeekStart
";

$stmt = sqlsrv_query($conn, $sql);

$data = [];
while ($row = sqlsrv_fetch_array($stmt, SQLSRV_FETCH_ASSOC)) {
    $data[] = [
        "week" => $row["WeekStart"]->format("Y-m-d"),
        "count" => (int)$row["TotalDocCnt"]
    ];
}

sqlsrv_close($conn);

echo json_encode($data);
