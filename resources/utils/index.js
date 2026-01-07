export function generateDayWiseTimeSeries(baseval, count, yrange) {
    let i = 0;
    const series = [];
    while (i < count) {
        const x = baseval;
        const y =
            Math.floor(Math.random() * (yrange.max - yrange.min + 1)) +
            yrange.min;
        series.push([x, y]);
        baseval += 86400000; // Add one day in milliseconds
        i++;
    }
    return series;
}
