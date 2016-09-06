function selectAllCombo(cmbOrigen){
	for (i = 0; i < cmbOrigen.options.length; i++)
		cmbOrigen.options[i].selected = true;
}

function selectNoneCombo(cmbOrigen){
	for (i = 0; i < cmbOrigen.options.length; i++)
		cmbOrigen.options[i].selected = false;
}

function allCombo(cmbOrigen, cmbDestino) {
	for (i = 0; i < cmbOrigen.options.length; i++)
		cmbOrigen.options[i].selected = true;
	addSelection(cmbOrigen,cmbDestino);
}

function addSelection(cmbOrigen, cmbDestino){
	if (cmbOrigen.options.selectedIndex != -1){
		for (i = 0; i < cmbOrigen.options.length; i++)
			if (cmbOrigen.options[i].selected) {
				cmbDestino.options.length = cmbDestino.options.length + 1;
				cmbDestino.options[cmbDestino.options.length - 1].text = cmbOrigen.options[i].text;
				cmbDestino.options[cmbDestino.options.length - 1].value = cmbOrigen.options[i].value;
			}
		i = 0;
		while (i < cmbOrigen.options.length){
			if (cmbOrigen.options[i].selected) {
				for (j = i; j < cmbOrigen.options.length - 1; j++) {
					cmbOrigen.options[j].text=cmbOrigen.options[j+1].text;
					cmbOrigen.options[j].value=cmbOrigen.options[j+1].value;
					cmbOrigen.options[j].selected=cmbOrigen.options[j+1].selected;
				}
				cmbOrigen.options.length = cmbOrigen.options.length - 1;
				i--;
			}
			i++;
		}
	}
}

function selectValueInCombo(cmbOrigen, intValue){
	blnFound = false;
	for (i = 0; i < cmbOrigen.options.length && !blnFound; i++){
		if (cmbOrigen.options[i].value == intValue){
			cmbOrigen.options.selectedIndex = i;
			blnFound = true;
		}
	}
}

function storeComboValues(objCombo, objText){
	objText.value = "";
	for(i = 0; i < objCombo.options.length; i++)
		objText.value += objCombo.options[i].value + (((i + 1) < objCombo.options.length) ? ';' : '');
}

function storeComboSelectedValues(objCombo, objText){
	objText.value = "";
	for (i = 0; i < objCombo.options.length; i++){
		if (objCombo.options[i].selected){
			objText.value += ((objText.value) ? ";" : "") + objCombo.options[i].value;
		}
	}
}