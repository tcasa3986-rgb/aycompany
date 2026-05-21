const express = require('express');
const router = express.Router();
const databaseController = require('../controllers/databaseController');
const multer = require('multer');
const path = require('path');
const fs = require('fs');

// Configure multer for file upload (temporarily stored in uploads/)
const uploadDir = path.join(__dirname, '..', 'uploads');
if (!fs.existsSync(uploadDir)) {
    fs.mkdirSync(uploadDir, { recursive: true });
}

const storage = multer.diskStorage({
    destination: (req, file, cb) => {
        cb(null, uploadDir);
    },
    filename: (req, file, cb) => {
        cb(null, `restore_${Date.now()}${path.extname(file.originalname)}`);
    }
});
const upload = multer({ storage: storage });

router.get('/backup', databaseController.backupDatabase);
router.post('/restore', upload.single('backupFile'), databaseController.restoreDatabase);
router.post('/reset', databaseController.resetDatabase);

module.exports = router;
