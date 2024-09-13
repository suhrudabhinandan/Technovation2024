// server.js
const express = require('express');
const app = express();
const port = 3000;

app.use(express.static('public')); // Serve static files (e.g., HTML, CSS)

// Sample data to simulate dynamic visitor stats
let visitorsToday = 2265;
let visitorsYesterday = 2324;
let visitorsLastWeek = 1324;
let visitorsThisMonth = 1551324;
let visitorsLastMonth = 12324123;

app.get('/api/visitor-stats', (req, res) => {
    // Simulate updating visitor stats on each request
    visitorsToday += Math.floor(Math.random() * 50); // Increment today's visitors by a random number

    const stats = {
        visitorsToday,
        visitorsYesterday,
        visitorsLastWeek,
        visitorsThisMonth,
        visitorsLastMonth,
        chartData: {
            labels: ['Mon', 'Tue', 'Wed', 'Thu', 'Fri', 'Sat', 'Sun'],
            values: [1000, 1200, 1500, 2000, 2200, 2300, visitorsToday]
        }
    };

    res.json(stats);
});

app.listen(port, () => {
    console.log(`Server running at http://localhost:${port}`);
});
