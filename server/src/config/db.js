require('dotenv').config();
const { Sequelize } = require('sequelize');

// Railway provee MYSQL_URL o variables individuales MYSQLHOST, etc.
const sequelize = process.env.MYSQL_URL
    ? new Sequelize(process.env.MYSQL_URL, { dialect: 'mysql', logging: false, timezone: '-05:00', pool: { max: 10, min: 0, acquire: 30000, idle: 10000 } })
    : new Sequelize(
        process.env.DB_NAME,
        process.env.DB_USER,
        process.env.DB_PASSWORD,
        {
            host:    process.env.DB_HOST,
            port:    process.env.DB_PORT || 3306,
            dialect: 'mysql',
            logging: false,
            timezone: '-05:00',
            pool: { max: 10, min: 0, acquire: 30000, idle: 10000 }
        }
    );

module.exports = sequelize;
