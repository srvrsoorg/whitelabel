export default{
    methods: {
        downloadCSV(data, columnMapping, filename = "data.csv") {
            if(!data || data.length === 0) {
                console.error("No data available to download.");
                return;
            }
            
            // Extract columns from the mapping
            const columns = Object.keys(columnMapping);
            const headers = Object.values(columnMapping);
        
            // Convert data to CSV format
            let csvContent = "\uFEFF" + headers.join(",") + "\n"; 
            csvContent += data.map(item => columns.map(column => `"${item[column] ?? ""}"`).join(",")).join("\n");
        
            // Create a Blob and trigger download
            const blob = new Blob([csvContent], { type: "text/csv;charset=utf-8;" });
            const url = URL.createObjectURL(blob);
        
            // Create a temporary link and trigger download
            const a = document.createElement("a");
            a.href = url;
            a.download = filename;
            document.body.appendChild(a);
            a.click();
            document.body.removeChild(a);
            URL.revokeObjectURL(url);
        }
    }
}