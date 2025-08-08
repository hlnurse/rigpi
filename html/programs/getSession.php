<?php
session_start();
?>
<!DOCTYPE html>
<html>
<head>
  <title>Session to JS Example</title>
</head>
<body>

<h1>Hello Page</h1>

<script>
  const sessionData = <?php echo json_encode($_SESSION); ?>;
  console.log("Inline session data:", sessionData);

  document.body.insertAdjacentHTML('beforeend', `<p>User: ${sessionData.myCall}</p>`);
</script>

</body>
</html>
