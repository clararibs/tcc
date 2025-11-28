<?php
include 'conexao.php';
$sql = "SELECT * FROM solicitacoes WHERE status = 'pendente'";
$result = $conn->query($sql);

while ($row = $result->fetch_assoc()):
?>
<div class="notification-item">
    <p><strong>Nome:</strong> <?= $row['nome'] ?></p>
    <p><strong>Email:</strong> <?= $row['email'] ?></p>
    <p><strong>Data:</strong> <?= $row['data'] ?></p>
    <p><strong>Hora:</strong> <?= $row['hora'] ?></p>

    <div class="notification-actions">
        <form method="POST" action="aceitar.php">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button class="accept-btn">Aceitar</button>
        </form>

        <form method="POST" action="recusar.php">
            <input type="hidden" name="id" value="<?= $row['id'] ?>">
            <button class="reject-btn">Recusar</button>
        </form>
    </div>
</div>
<?php endwhile; ?>
