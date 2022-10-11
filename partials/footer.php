<script src="<?= dirname($_SERVER['SERVER_ADDR']) ?>/assets/js/main.js"></script>
</body>

</html>

<?php
if (session_id()) {
    unset($_SESSION['errors']);
    unset($_SESSION['form_data']);
}
?>