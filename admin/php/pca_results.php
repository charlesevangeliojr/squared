<?php
// pca_results.php
session_start();
require_once 'config.php';
header('Content-Type: application/json');

$award_slug = $_GET['award'] ?? '';
if (!preg_match('/^[a-z0-9\-]{3,64}$/i', $award_slug)) {
  http_response_code(422);
  echo json_encode(['ok' => false, 'error' => 'invalid_award']);
  exit;
}

$stmt = $conn->prepare("
  SELECT a.title,
         n.id AS nominee_id,
         n.name,
         COALESCE(v.cnt, 0) AS votes
  FROM pca_awards a
  JOIN pca_nominees n ON n.award_id = a.id
  LEFT JOIN (
     SELECT nominee_id, COUNT(*) AS cnt
     FROM pca_votes
     WHERE award_id = (SELECT id FROM pca_awards WHERE slug = ?)
     GROUP BY nominee_id
  ) v ON v.nominee_id = n.id
  WHERE a.slug = ?
  ORDER BY votes DESC, n.name ASC
");
$stmt->bind_param("ss", $award_slug, $award_slug);
$stmt->execute();
$res = $stmt->get_result();

$data = [];
$title = null;
while ($row = $res->fetch_assoc()) {
  $title = $row['title'];
  $data[] = [
    'nominee_id' => (int)$row['nominee_id'],
    'name'       => $row['name'],
    'votes'      => (int)$row['votes'],
  ];
}
echo json_encode(['ok' => true, 'award' => $award_slug, 'title' => $title, 'results' => $data]);
