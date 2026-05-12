$(function () {
    var $logoutLink = $("#admin-logout-link");
    var $logoutForm = $("#admin-logout-form");
    if ($logoutLink.length && $logoutForm.length) {
        $logoutLink.on("click", function (e) {
            e.preventDefault();
            $logoutForm.submit();
        });
    }
});
