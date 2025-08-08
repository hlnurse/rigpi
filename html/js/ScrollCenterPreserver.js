export default class ScrollCenterPreserver {
  constructor(container, table) {
    this.container = container;
    this.table = table;
  }

  getCenterRow() {
    const containerRect = this.container.getBoundingClientRect();
    const containerCenter = containerRect.top + containerRect.height / 2;

    let closestRow = null;
    let minDistance = Infinity;
if (table){
    Array.from(this.table.rows).forEach(row => {
      const rowRect = row.getBoundingClientRect();
      const rowCenter = rowRect.top + rowRect.height / 2;
      const distance = Math.abs(rowCenter - containerCenter);

      if (distance < minDistance) {
        minDistance = distance;
        closestRow = row;
      }
    });

    return closestRow;
  }else{
    return 0;
  }
  }

  preserveAndUpdate(updateCallback) {
    const centerRow = this.getCenterRow();
    const oldTop = centerRow ? centerRow.getBoundingClientRect().top : 0;

    updateCallback();

    const newTop = centerRow ? centerRow.getBoundingClientRect().top : 0;
    const scrollDiff = newTop - oldTop;

    this.container.scrollTop += scrollDiff;

    // Optional: Highlight it
    if (centerRow) {
      Array.from(this.table.rows).forEach(r => r.classList.remove('highlight'));
      centerRow.classList.add('highlight');
    }
  }
}
