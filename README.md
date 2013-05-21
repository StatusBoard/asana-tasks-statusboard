asana-tasks-statusboard
=======================

A simple widget for Panic's status board app for iPad. Will fetch all users in an Asana workspace and get all tasks assigned to them which are not done, done and past due and output them in neat bar graph.

![Screenshot](asana-tasks-statusboard.png)

### Instructions

Update the ASANA_API_KEY & ASANA_WORKSPACE_ID contants, upload it to a server.

### Notes

I've hacked [this github repo by Ajimix](https://github.com/ajimix/asana-api-php-class) to include $opt_fields for the getTasksByFilter() method. Doing so makes it easy to fields like completed and due_on to figure out who has tasks which are overdue, completed or to do.

### Further Reading

You can read more about this on my [blog](http://blog.eoghanobrien.com/123456789/asana-tasks-per-user-in-statusboard).

Enjoy