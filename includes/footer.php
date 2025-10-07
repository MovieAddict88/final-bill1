		</div> <!-- container -->
		</div> <!-- .content-wrapper -->
	</main> <!-- .cd-main-content -->
<script src="component/js/jquery-2.1.4.js"></script>
<script src="component/js/bootstrap.js"></script>
<!-- <script src="component/js/jquery.bootgrid.js"></script>  -->
<script src="component/js/jquery.menu-aim.js"></script>
<script src="component/js/main.js"></script>
<script src="component/js/chart.js"></script>
<!-- <script src="component/js/tether.min.js"></script> -->

<?php if ($_SESSION['user_role'] == 'admin'): ?>
<script>
$(document).ready(function() {
    var notificationsContainer = $('#notifications-container');

    function fetchNotifications() {
        $.ajax({
            url: 'get_notifications.php',
            method: 'GET',
            dataType: 'json',
            success: function(notifications) {
                var notificationList = $('#notification-list');
                var notificationCount = $('#notification-count');

                if (notifications.length > 0) {
                    notificationList.empty();
                    notifications.forEach(function(notification) {
                        var listItem = '<li><a href="view_payment.php?payment_id=' + notification.id + '">Payment from ' + notification.full_name + ' needs approval.</a></li>';
                        notificationList.append(listItem);
                    });
                    notificationCount.text(notifications.length).show();
                } else {
                    notificationCount.hide();
                    // If there are no notifications, ensure the dropdown is not shown
                    notificationsContainer.removeClass('selected');
                }
            },
            error: function(xhr, status, error) {
                console.error('Error fetching notifications:', error);
            }
        });
    }

    // Click handler for the notification bell
    notificationsContainer.children('a').on('click', function(event) {
        event.preventDefault();
        // Toggle the 'selected' class to show/hide the dropdown
        notificationsContainer.toggleClass('selected');
        // Hide other dropdowns
        $('.account').removeClass('selected');
    });

    // Close dropdown when clicking elsewhere
    $(document).on('click', function(event){
        if (!$(event.target).closest('#notifications-container').length) {
            notificationsContainer.removeClass('selected');
        }
    });

    // Fetch notifications every 30 seconds
    setInterval(fetchNotifications, 30000);

    // Initial fetch
    fetchNotifications();
});
</script>
<?php endif; ?>
</body>
</html>