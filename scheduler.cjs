const { spawn } = require("child_process");
const path = require("path");
const fs = require("fs");

const tempDir = path.join(__dirname, "storage", "temp");
if (!fs.existsSync(tempDir)) {
    fs.mkdirSync(tempDir, { recursive: true });
}

function runSchedule() {
    const child = spawn("php", ["artisan", "schedule:run"], {
        cwd: __dirname,
        windowsHide: true,
        shell: false,
        env: {
            ...process.env,
            TEMP: tempDir,
            TMP: tempDir,
        },
        stdio: "inherit",
    });

    child.on("error", (err) => {
        console.error(`[Scheduler Error] ${err.message}`);
    });
}

// Jalankan pertama kali saat service mulai
runSchedule();

// Jalankan setiap 60 detik tepat
const intervalId = setInterval(runSchedule, 60 * 1000);

// Graceful shutdown untuk PM2
function shutdown() {
    clearInterval(intervalId);
    process.exit(0);
}

process.on("SIGINT", shutdown);
process.on("SIGTERM", shutdown);
