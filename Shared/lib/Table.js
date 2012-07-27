function Table(tableName){
	this.Name = tableName;
	this.Data = new Array();
	this.ColumnNames = new Array();
	this.NumberOfRows = 0;
	
	this.AddRow = function(row){
		this.Data.push(row);
		this.NumberOfRows++;
	};
	
	this.AddColumn = function(newCol){
		this.ColumnNames.push(newCol);
	};
	
	this.ToHtml = function(offset, limit){
		if(limit <= 0){
			limit = this.Data.length;
		}
		var table = '';
		table += '<table id="listcostumer-table" border="0" cellspacing="0" cellpadding="0">';
		table += '<thead><tr>';
		for(var i = 0; i < this.ColumnNames.length; i++){
			table += '<th><div onclick="javascript:page.TableSort(\''+this.ColumnNames[i]+'\')" style="cursor: pointer;">' + this.ColumnNames[i] + '</div></th>';
		}
		table += '</tr></thead><tbody>';
		for(var j = offset; j < this.Data.length && j < limit; j++){
			table += '<tr>';
			for(var k = 0; k < this.Data[j].length; k++){
				table += '<td>' + this.Data[j][k] + '</td>';
			}
			table += '</tr>';
		}
		table += '</tbody><tfoot><tr><td colspan="'+this.ColumnNames.length+'"></td></tr></tfoot>';
		table += '</table>';
		return table;
	};
	
	this.GetColumnIndex = function(columnName){
		//Find the associated index of the column.
		var index = 0;
		var found = false;
		for(var i = 0; i < this.ColumnNames.length; i++){
			if(this.ColumnNames[i] == columnName){
				index = i;
				found = true;
				break;
			}
		}
		if(found){
			return index;
		}
		else{
			return -1;
		}
	}
	
	this.Swap = function(i, j){
		if(i > this.Data.length || j > this.Data.length){
			return;
		}
		var a = this.Data[i];
		var b = this.Data[j];
		this.Data[i] = b;
		this.Data[j] = a;
	}
	
	this.isLessThan = function(i,j,index){
		if(i >= this.Data.length || j >= this.Data.length){
			return;
		}
		return this.Data[i][index] < this.Data[j][index];
	}
	this.isGreatherThan = function(i,j,index){
		if(i >= this.Data.length || j >= this.Data.length){
			return;
		}
		return this.Data[i][index] > this.Data[j][index];
	}
	
	this.QuickSortAll = function(columnName,asc){
		this.QuickSort(columnName, asc, 0, this.Data.length-1);
	}
	this.QuickSort = function(columnName, asc, left, right){
	/*
	// See "Choice of pivot" section below for possible choices
          choose any 'pivotIndex' such that 'left' <= 'pivotIndex' <= 'right'
 
          // Get lists of bigger and smaller items and final position of pivot
          'pivotNewIndex' := partition(array, 'left', 'right', 'pivotIndex')
 
          // Recursively sort elements smaller than the pivot
          quicksort(array, 'left', 'pivotNewIndex' - 1)
 
          // Recursively sort elements at least as big as the pivot
          quicksort(array, 'pivotNewIndex' + 1, 'right')
	*/
		var index = this.GetColumnIndex(columnName);
		var length = right - left + 1;
		if(index == -1 || length < 2){
			return;
		}
		var pivotIndex = left + Math.floor(length/2);
		
		var pivotNewIndex = this.Partition(columnName, asc, left,right, pivotIndex);
		
		this.QuickSort(columnName, asc, left, pivotNewIndex-1);
		this.QuickSort(columnName, asc, pivotNewIndex+1, right);
	}
	
	this.Partition = function(columnName, asc, leftIndex, rightIndex, pivotIndex){
		/*
		'pivotValue' := array['pivotIndex']
		  swap array['pivotIndex'] and array['right']  // Move pivot to end
		  'storeIndex' := 'left'
		  for 'i' from 'left' to 'right' - 1  // left = i < right
			  if array['i'] < 'pivotValue'
				  swap array['i'] and array['storeIndex']
				  'storeIndex' := 'storeIndex' + 1
		  swap array['storeIndex'] and array['right']  // Move pivot to its final place
		  return 'storeIndex'
		*/
		var columnIndex = this.GetColumnIndex(columnName);
		var length = rightIndex-leftIndex + 1;
		if(length <= 1){
			return;
		}
		this.Swap(pivotIndex, rightIndex);
		pivotIndex = rightIndex;
		
		var storeIndex = leftIndex;
		for(var i = leftIndex; i < rightIndex; i++){
			if(asc){
				if(this.isLessThan(i,pivotIndex,columnIndex)){
					this.Swap(i, storeIndex);
					storeIndex++;
				}
			}
			else{
				if(this.isGreatherThan(i,pivotIndex,columnIndex)){
					this.Swap(i, storeIndex);
					storeIndex++;
				}
			}
		}
		this.Swap(storeIndex, pivotIndex);
		
		return storeIndex;
	}
}

function XMLToTable(Xml){
	var titles=Xml.getElementsByTagName("title");
	
	var table = new Table('');
	for(var i = 0; i < titles.length; i++){
		table.AddColumn(titles[i].childNodes[0].nodeValue);
	}
	
	var rows = Xml.getElementsByTagName("row");
	for(var i = 0; i < rows.length; i++){
		var row = rows[i];
		var cells = row.getElementsByTagName("cell");
		var rowArray = new Array();
		for(var j = 0; j < cells.length; j++){
			rowArray.push(cells[j].childNodes[0].nodeValue);
		}
		table.AddRow(rowArray);
	}
	
	return table;
}