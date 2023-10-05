<?php

/**
 * @version    CVS: 0.1.0
 * @package    Com_Chirp
 * @author     Nathan Schneider <nathan@quirkable.io>
 * @copyright  2023 Quirkable
 * @license    GNU General Public License version 2 or later; see LICENSE.txt
 */

// No direct access
defined('_JEXEC') or die;

$clickData = $this->getModel()->clickdata("easyshop_orders");

?>
<header>
	<div>
		<h1>Chirp 🐦 Your Ultimate Social Proof Solution for Joomla!</h1>
	</div>
</header>
<hr />
<section>
	<div id="chirp_dash_left" style="position:relative;height:300px">
		<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
		<canvas id="myChart"></canvas>
		<script>
			function convertJSONToChartData(jsonString) {
				// Parse the JSON string into an array of objects
				const data = JSON.parse(jsonString);

				// Create a map to store the count of unique click_ids for each order_id
				const orderClickCountMap = new Map();

				// Calculate the count of unique click_ids for each order_id
				data.forEach(entry => {
					const order_id = entry.order_id;
					if (!orderClickCountMap.has(order_id)) {
						orderClickCountMap.set(order_id, new Set());
					}
					orderClickCountMap.get(order_id).add(entry.click_id);
				});

				// Extract data for the chart
				const labels = Array.from(orderClickCountMap.keys()); // X-axis labels (order_ids)
				const values = Array.from(orderClickCountMap.values()).map(set => set.size); // Y-axis values (count of unique click_ids)

				// Create the chart data object
				const chartData = {
					labels: labels,
					datasets: [{
						label: 'Clicks per Order',
						data: values,
						backgroundColor: 'rgba(75, 192, 192, 0.5)', // Bar color with transparency
						borderColor: 'rgba(75, 192, 192, 1)', // Border color
						borderWidth: 1, // Border width
					}, ],
				};

				return chartData;
			}
			const clickData = `<?php echo $clickData; ?>`;
			const chartData = convertJSONToChartData(clickData);

			// Create a chart using Chart.js
			const ctx = document.getElementById('myChart').getContext('2d');
			new Chart(ctx, {
				type: 'bar',
				data: chartData,
				options: {
					scales: {
						y: {
							ticks: {
								precision: 0
							}
						}
					}
				}
			});
		</script>
	</div>

	<div id="chirp_dash_right">

	</div>
</section>
