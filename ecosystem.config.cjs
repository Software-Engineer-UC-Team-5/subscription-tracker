module.exports = {
    apps: [
        {
            name: "subscriptiontracker-queue",
            script: "artisan",
            interpreter: "php",
            args: "queue:work --sleep=3 --tries=3 --max-time=3600",
            exec_mode: "fork",
            autorestart: true,
            watch: false,
            max_memory_restart: "200M",
        },
    ],
};
