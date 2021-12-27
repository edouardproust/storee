let rows = document.querySelectorAll(".clickable-row");
rows.forEach(row => {
    row.addEventListener('click', () => {
        document.location.href = row.getAttribute('href')
    })
    
})