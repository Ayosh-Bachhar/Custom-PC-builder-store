// State object to keep track of current selections
const state = { cpu: null, mobo: null, ram: null, storage: null, psu: null, gpu: null };

// 1. Populate Dropdowns on Load
window.onload = () => {
    populateSelect('select_cpu', productsData['CPU']);
    populateSelect('select_mobo', productsData['Motherboard']);
    populateSelect('select_ram', productsData['RAM']);
    populateSelect('select_storage', productsData['Storage']);
    populateSelect('select_psu', productsData['Power Supply']);
    populateSelect('select_gpu', productsData['Graphics Card']);
};

function populateSelect(elementId, items) {
    const select = document.getElementById(elementId);
    if (!items) return; 
    
    items.forEach(item => {
        const option = document.createElement('option');
        option.value = item.Product_ID;
        // Store JSON string in dataset for easy retrieval later
        option.dataset.info = JSON.stringify(item); 
        option.textContent = `${item.Name} - ${item.Price} ৳`;
        select.appendChild(option);
    });
}

// 2. Main Logic: Called every time a dropdown changes
function updateBuild() {
    // Update State
    state.cpu = getSelectionData('select_cpu');
    state.mobo = getSelectionData('select_mobo');
    state.ram = getSelectionData('select_ram');
    state.storage = getSelectionData('select_storage');
    state.psu = getSelectionData('select_psu');
    state.gpu = getSelectionData('select_gpu');

    // Update Hidden Form Inputs
    document.getElementById('form_cpu').value = state.cpu ? state.cpu.Product_ID : '';
    document.getElementById('form_mobo').value = state.mobo ? state.mobo.Product_ID : '';
    document.getElementById('form_ram').value = state.ram ? state.ram.Product_ID : '';
    document.getElementById('form_storage').value = state.storage ? state.storage.Product_ID : '';
    document.getElementById('form_psu').value = state.psu ? state.psu.Product_ID : '';
    document.getElementById('form_gpu').value = state.gpu ? state.gpu.Product_ID : '';

    checkCompatibility();
    updateSummary();
}

function getSelectionData(elementId) {
    const select = document.getElementById(elementId);
    const selectedOpt = select.options[select.selectedIndex];
    return selectedOpt.value ? JSON.parse(selectedOpt.dataset.info) : null;
}

// 3. Compatibility Checker
function checkCompatibility() {
    // Check Socket (CPU vs MOBO)
    const socketWarn = document.getElementById('warn_socket');
    if (state.cpu && state.mobo && state.cpu.Socket && state.mobo.Socket) {
        socketWarn.style.display = (state.cpu.Socket !== state.mobo.Socket) ? 'block' : 'none';
    } else {
        socketWarn.style.display = 'none';
    }

    // Check RAM Type (RAM vs MOBO)
    const ramWarn = document.getElementById('warn_ram');
    if (state.ram && state.mobo && state.ram.RAM_Type && state.mobo.RAM_Type) {
        ramWarn.style.display = (state.ram.RAM_Type !== state.mobo.RAM_Type) ? 'block' : 'none';
    } else {
        ramWarn.style.display = 'none';
    }
}

// 4. Update Price and Wattage
function updateSummary() {
    const summaryList = document.getElementById('summary_list');
    summaryList.innerHTML = ''; 
    
    let totalPrice = 0;
    let totalWattage = 0;

    const components = Object.values(state).filter(item => item !== null);

    components.forEach(item => {
        totalPrice += parseFloat(item.Price);
        totalWattage += parseInt(item.Watt_Value || 0);

        const div = document.createElement('div');
        div.className = 'summary-item';
        div.innerHTML = `<span>${item.Category_Name}: ${item.Name}</span> <span>${item.Price} ৳</span>`;
        summaryList.appendChild(div);
    });

    document.getElementById('total_price').innerText = totalPrice.toFixed(2);
    document.getElementById('total_wattage').innerText = totalWattage;
}

// 5. Validation for Required Components before Add To Cart
function validateRequired() {
    if (!state.cpu || !state.mobo || !state.ram || !state.storage || !state.psu) {
        alert("Please select all required components (CPU, Motherboard, RAM, Storage, and Power Supply) before adding to cart.");
        return false;
    }
    return true;
}

// 6. Export to Text File
function exportToText() {
    const components = Object.values(state).filter(item => item !== null);
    if (components.length === 0) {
        alert("Please select at least one component to export.");
        return;
    }

    let textContent = "=================================\n";
    textContent += "        CUSTOM PC BUILD          \n";
    textContent += "=================================\n\n";

    components.forEach(item => {
        textContent += `[${item.Category_Name}] ${item.Name}\n`;
        textContent += `Price: ${item.Price} ৳ | Wattage: ${item.Watt_Value || 0}W\n\n`;
    });

    const totalPrice = document.getElementById('total_price').innerText;
    const totalWattage = document.getElementById('total_wattage').innerText;

    textContent += "---------------------------------\n";
    textContent += `TOTAL ESTIMATED WATTAGE: ${totalWattage} W\n`;
    textContent += `TOTAL PRICE: ${totalPrice} ৳\n`;
    textContent += "=================================\n";

    const blob = new Blob([textContent], { type: "text/plain" });
    const link = document.createElement("a");
    link.href = URL.createObjectURL(blob);
    link.download = "My_PC_Build.txt";
    link.click();
}
