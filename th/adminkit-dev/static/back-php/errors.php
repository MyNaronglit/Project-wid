<?php if (isset($_SESSION['errors']) && count($_SESSION['errors']) > 0) : ?>
    <div class="error">
        <?php foreach ($_SESSION['errors'] as $error) : ?>
            <p><?php echo $error ?></p>
        <?php endforeach ?>
        <?php unset($_SESSION['errors']); ?>
    </div>
<?php endif ?>

<?php if (isset($_SESSION['success'])) : ?>
    <div class="success">
        <p><?php echo $_SESSION['success']; ?></p>
        <?php unset($_SESSION['success']); ?>
    </div>
<?php endif ?>