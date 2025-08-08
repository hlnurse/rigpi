export class TableScroller {
  constructor(containerId, tableId, enableKeyboard = true) {
	this.container = document.getElementById(containerId);
	this.table = document.getElementById(tableId);
	this.enableKeyboard = enableKeyboard;
	this.debounceTime = 500;
	this.timeout = null;

	this.scrollLock = 4; // Default mode: center selection
	this.init();
  }

  init() {
	if (!this.container || !this.table) {
	  console.warn('TableScroller: container or table not found.');
	  return;
	}

	this.container.addEventListener("scroll", () => {
	  this.debounce(() => this.restoreCenteredRow());
	});

	if (this.enableKeyboard) {
	  window.addEventListener("keydown", (event) => {
		if (event.key === "ArrowUp" || event.key === "ArrowDown") {
		  console.log("TableScroller: key press detected.");
		  this.debounce(() => this.restoreCenteredRow());
		}
	  });
	}
  }

  debounce(func) {
	clearTimeout(this.timeout);
	this.timeout = setTimeout(func, this.debounceTime);
  }

  restoreCenteredRow() {
	if (this.scrollLock !== 4) return;

	const rows = this.table.querySelectorAll("tr");
	if (rows.length === 0) return;

	const middleIndex = Math.floor(rows.length / 2);
	const centerRow = rows[middleIndex];
	if (!centerRow) return;

	const rowID4 = centerRow.getAttribute("id");
	const rowdx = centerRow.getAttribute("call");
	console.log('Scrolled to ' + rowdx + ' at row ' + rowID4);

	if (rowID4) {
	  const updatedRow = document.getElementById(rowID4);
	  if (updatedRow) {
		this.container.scrollTop = updatedRow.offsetTop - (this.container.clientHeight / 2) + (updatedRow.clientHeight / 2);
		console.log('Centered at ' + this.container.scrollTop);
	  }
	}
  }

  highlightCenteredRow() {
	this.restoreCenteredRow();
  }

  scrollToTop() {
	this.container.scrollTop = 0;
  }

  scrollToBottom() {
	this.container.scrollTop = this.container.scrollHeight;
  }

  refresh() {
	this.restoreCenteredRow();
  }
}
