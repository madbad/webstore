//https://developer.mozilla.org/en-US/docs/Web/API/Web_components/Using_custom_elements

// Create a class for the element
class XMenu extends HTMLElement {
  static observedAttributes = ["mtitle", "items"];

  constructor() {
    // Always call super first in constructor
    super();
    console.log(this);
    this._shadow = this.attachShadow({ mode: "open" });
//    this._shadow.innerHTML=`${mtitle}`;
/*
    var test =`
    <script src="./../libs/drag.js"></script>
	<template>
		<style>
		:host {

			border: 1px solid #a1a1a1;
			color: #000000;
			display: inline-block;
			min-width:15em;
			max-width:90%;
			position:relative;
			background-color:white;
			border-radius: 0.8em;
			overflow:hidden;
			z-index: 100;
			margin-left:50%
			margin-rigth:50%;
			visibility: hidden;
			position:fixed;
			border-collapse:separate;
		}
		
		.multipleselected {
			color:green;
			font-weight: bold;
		}
		.selected {
			background-image: none;
			border-color:orange;
			outline: none;
			box-shadow: 0 0 10px orange;
			background-color: orange;
		}
		.selected td{
			background-color: orange;
		}
		.hidden {
			width: 0px;
			height: 0px;
			visibility:hidden;
			display:none;
			overflow:hidden;
		}
		.title {
			background: linear-gradient(to bottom, #cdd2d8 8%,#989a9b 100%);
			text-shadow:#CECECE 0px 1px 0, #000 0 -1px 0;
			font-weight:bold;
			font-size:0.8em;
			color: #757575;
			text-align: center;
		}
		.title span {
			position:absolute;
			top:0px;
			right:1em; 
		}
		.title input {
			padding:0.4em;
			border-radius: 0.4em;
			color: grey;
			font-size:1.2em;
			width:90%;
			margin:0.8em;
			margin-top:0px;
		}
		table, thead, tbody, tfooter {
			width: 100%;
			border: 0px;
			padding:0px;
			margin:0px;
			border-spacing: 0;
		}
		thead {
			font-weight:bold;
			color:white;
			font-size: 0.8em;
		}
		thead td{
			background: linear-gradient(15deg, #616161 0%,#616161 4%,#7c7c7c 100%);
			padding: 0.2em;
		}
		tbody tr {
			background-image: linear-gradient(to bottom, #ffffff 0%,#ffffff 88%,#eaeaea 100%);
		}
		tbody td {
			border-left: 1px solid #e1e1e1;
			padding: 0.2em;
		}
		.hiddenColumn #foo.hide2 tr *:nth-child(2) {
    display: none;
}

		</style>

		<div class="title">
			<div id="dragger" class="dragger">${title} <span>pag.${page}/${pages}</span></div>
			<input 
				id="searchfield"
				placeholder="Digita qui per cercare..."
				type="text"
				tabindex="0"
				value="${searchstring-change}"
				on-focus="onInputFocus"
				on-blur="onInputBlur"
				on-keyup="onInputKeyup"
			>
		</div>
		<div id="items">
			<table id="table">
				<thead id="tableHead">
					<template is="dom-repeat" items="${visibleitems}">
						<template is="dom-if" if="${_isFirst(index)}">
						<tr>
							<template is="dom-repeat" items="${_itemToArray(item)}">
								<td>
									${item.key}
								</td>
							</template> 
						</tr>
						</template>
					</template>
				</thead>
				<tbody id="tableBody">
					<template is="dom-repeat" items="${visibleitems}" id="mymenuitems">
					<tr>
						<template is="dom-repeat" items="${_itemToArray(item)}">
							<td>
								${item.value}
							</td>
						</template> 
					</tr>
					</template> 
				</tbody>
				<tfoot id="tableFooter"></tfoot>
			</table>
		</div>
    `;
*/
  }

  connectedCallback() {
    console.log("Custom element added to page.");
    this.setAttribute("mtitle","JS title");
    this.items=[];

  }

  disconnectedCallback() {
    console.log("Custom element removed from page.");
  }

  adoptedCallback() {
    console.log("Custom element moved to new page.");
  }

  attributeChangedCallback(name, oldValue, newValue) {
    console.log(`Attribute ${name} has changed.`);
    console.log(`    old ${oldValue}`);
    console.log(`    new ${newValue}`);
    console.log(`    new ${this.getAttribute('mtitle')}`);    
    console.log(this);
//    this._shadow = this.attachShadow({ mode: "open" });
console.log(this.hasOwnProperty('_shadow'));
if(this.hasOwnProperty('_shadow')){
	this._shadow.innerHTML=`<b>${this.getAttribute('mtitle')}<b>`;	
}

	/*
    if(name='menuitems'){
		console.log(this.innerHTML)
		this._shadow.innerHTML='';
		console.log(newValue);
//		try {
			const items = JSON.parse(newValue);
			console.log(items);
			for (var i=0; i<items.length; i++){
				var item = items[i];
				this._shadow.innerHTML+=`<ul><a href="${item.url}">${item.name}</a></ul>`;
			}
//		} catch (error) {
//			console.log('invalid json provided');
//		}
*/
	}
}

customElements.define("x-menu", XMenu);
/*----------------------------------------------------------------------------------------------------*/
