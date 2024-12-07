<!DOCTYPE html>
<html>
<head>
    <title>PoE Ninja Gems</title>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <style>
        table {
            border-collapse: collapse;
            width: 100%;
            margin-bottom: 20px;
        }
        th, td {
            border: 1px solid #ddd;
            padding: 8px;
            text-align: left;
        }
        th {
            background-color: #f2f2f2;
        }
        tr:nth-child(even) {
            background-color: #f9f9f9;
        }
        .gem-icon {
            width: 32px;
            height: 32px;
            vertical-align: middle;
        }
        .filter-section {
            margin: 20px 0;
        }
        select {
            padding: 5px;
            font-size: 16px;
        }
        .price-up {
            color: green;
        }
        .price-down {
            color: red;
        }
        td {
            white-space: nowrap;
        }
    </style>
</head>
<body>
    <h1>PoE Ninja Gems</h1>

    <div class="filter-section">
        <label for="gemFilter">Filter gems: </label>
        <select id="gemFilter" onchange="filterGems()">
            <option value="all">All Gems</option>
            <option value="awakened">Awakened Gems Only</option>
            <option value="exceptional">Exceptional Gems</option>
            <option value="normal">Normal Gems Only</option>
        </select>
    </div>

    <table id="gemsTable">
        <thead>
            <tr>
                <th>Icon</th>
                <th>Name</th>
                <th>Level/Quality</th>
                <th>Chaos Value</th>
                <th>Divine Value</th>
                <th>Price Change</th>
                <th>Listings</th>
            </tr>
        </thead>
        <tbody></tbody>
    </table>

    <script>
        let allGems = [];
        const exceptionalGems = ['Empower Support', 'Enlighten Support', 'Enhance Support'];

        async function fetchGems() {
            try {
                console.log('Fetching gems...');
                const response = await fetch('/gems-data');
                console.log('Response status:', response.status);
                
                if (!response.ok) {
                    throw new Error(`HTTP error! status: ${response.status}`);
                }
                
                const data = await response.json();
                console.log('Received data:', data);
                
                if (data.lines && Array.isArray(data.lines)) {
                    allGems = data.lines.filter(gem => !gem.corrupted);
                    console.log('Filtered gems:', allGems);
                    filterGems();
                } else {
                    throw new Error('Invalid data format received');
                }
            } catch (error) {
                console.error('Error:', error);
                document.body.innerHTML += `<p style="color: red">Erreur: ${error.message}</p>`;
            }
        }

        function filterGems() {
            const filterValue = document.getElementById('gemFilter').value;
            const tableBody = document.querySelector('#gemsTable tbody');
            let filteredGems = [];

            switch(filterValue) {
                case 'awakened':
                    filteredGems = allGems.filter(gem => gem.name.startsWith('Awakened'));
                    break;
                case 'exceptional':
                    filteredGems = allGems.filter(gem => exceptionalGems.includes(gem.name));
                    break;
                case 'normal':
                    filteredGems = allGems.filter(gem => 
                        !gem.name.startsWith('Awakened') && 
                        !exceptionalGems.includes(gem.name)
                    );
                    break;
                default:
                    filteredGems = allGems;
            }

            tableBody.innerHTML = filteredGems.map(gem => `
                <tr>
                    <td><img src="${gem.icon}" class="gem-icon" alt="${gem.name}"></td>
                    <td>${gem.name}${gem.variant ? ` (${gem.variant})` : ''}</td>
                    <td>${gem.levelQuality}</td>
                    <td>${gem.chaosValue.toLocaleString()}</td>
                    <td>${gem.divineValue.toLocaleString()}</td>
                    <td class="${gem.priceChange > 0 ? 'price-up' : 'price-down'}">${gem.priceChange}%</td>
                    <td>${gem.listings || 'N/A'}</td>
                </tr>
            `).join('');
        }

        document.addEventListener('DOMContentLoaded', fetchGems);
    </script>
</body>
</html>
