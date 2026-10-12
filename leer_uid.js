setInterval(() => {
    fetch("../api/leer_uid.php?" + Date.now())
        .then(res => res.text())
        .then(uid => {
            uid = uid.trim();
            if(uid !== ""){
                document.getElementById("uid").value = uid;
            }
        })
        .catch(err => console.log("Error leyendo UID:", err));
}, 800); // lectura cada 800ms


