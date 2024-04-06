const templateSelect = document.getElementById("template");
const xhr = new XMLHttpRequest();

function changeOption(){
    const selectedOption = templateSelect.options[templateSelect.selectedIndex];

    xhr.open("GET", "/change_template/" + selectedOption.text);
    xhr.send();
    xhr.responseType = "json";
    xhr.onload = () => {
        if (xhr.readyState === 4 && xhr.status === 200) {
            location.reload()
        } else {
            console.log(`Error: ${xhr.status}`);
        }
    };

    console.log("Вы выбрали: " + selectedOption.text)
}

templateSelect.addEventListener("change", changeOption);
