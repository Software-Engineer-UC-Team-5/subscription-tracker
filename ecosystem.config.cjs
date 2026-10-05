const path = require("path");
const fs = require("fs");

const tempDir = path.join(__dirname, "storage", "temp");
if (!fs.existsSync(tempDir)) {
    fs.mkdirSync(tempDir, { recursive: true });
}

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
            env: {
                TEMP: tempDir,
                TMP: tempDir,
            },
        },
        {
            name: "subscriptiontracker-scheduler",
            script: "artisan",
            interpreter: "php",
            args: "schedule:work",
            exec_mode: "fork",
            autorestart: true,
            watch: false,
            max_memory_restart: "150M",
            env: {
                TEMP: tempDir,
                TMP: tempDir,
            },
        },
    ],
};
