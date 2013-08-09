<style>
    html, body {
        width: 100%;
        height: 100%;
        margin: 0;
    }
    .columns {
        height: 95%;
        overflow: hidden;
        margin: 10px;
    }
    .column {
        -webkit-box-sizing: border-box;
        -moz-box-sizing: border-box;
        box-sizing: border-box;
        width: 20%;
        height: 100%;
        margin: 0 2.5%;
        padding: 5px;
        float: left;
        border: 1px solid #aaa;
    }

    .column.source.over {
        border: 1px solid #222;
    }

    .column table {
        width: 100%;
        border-collapse: collapse;
    }
    .column table tr {
        height: 28px;
    }
    .column table td {
        border: 1px dotted #aaa;
        height: 28px;
    }
    .column table td.over {
        border: 1px solid #444;
    }

    .node {
        border: 1px solid #333;
        background: #f0f0f0;
        padding: 5px;
        height: 25px;
        font: 18px/150% tahoma, sans-serif;
        overflow: hidden;
        cursor: move;
    }

    .span2 { height: 50px }
    .span3 { height: 75px }
    .span4 { height: 100px }
    .span6 { height: 150px }
    .span8 { height: 200px }

    .node + .node {
        margin-top: 4px;
    }

    .hidden {
        /*display: none;*/
    }
</style>

<div class="columns">
    <div class="source column">
        <div class="node span2" draggable="true" data-span="2">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node span3" draggable="true" data-span="3">Some long piece of text...</div>
        <div class="node span8" draggable="true" data-span="8">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node span4" draggable="true" data-span="4">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node span2" draggable="true" data-span="2">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node span6" draggable="true" data-span="6">Some long piece of text...</div>
    </div>
    <div class="target column">
        <table data-index="1">
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
        </table>
    </div>
    <div class="target column">
        <table data-index="2">
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
        </table>
    </div>
    <div class="target column">
        <table data-index="3">
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
            <tr><td></td></tr>
        </table>
    </div>
</div>

<script type="text/javascript">
    var nodes = document.querySelectorAll('.source .node'),
        cells = document.querySelectorAll('.target td'),
        source = document.querySelector('.source'),
        foreach = Array.prototype.forEach,
        listeners,
        dragging;

    listeners = {
        dragstart: function(e) {
            this.classList.add('hidden');
            dragging = this;
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.innerHTML);
        },
        dragend: function(e) {
            var hidden = document.querySelector('.node.hidden');
            if (hidden) {
                hidden.classList.remove('hidden');
            }
        },
        dragenter: function(e) {
            e.dataTransfer.dropEffect = 'move';
            if (this.tagName == 'TD' && isAcceptable(this, +dragging.getAttribute('data-span') || 1)) {
                this.classList.add('over');
            }
        },
        dragover: function(e) {
            e.preventDefault();
            e.dataTransfer.dropEffect = 'move';
        },
        dragleave: function(e) {
            this.classList.remove('over');
        },
        drop: function(e) {
            e.stopPropagation();
            e.preventDefault();

            this.classList.remove('over');

            if (dragging) {
                var span = +dragging.getAttribute('data-span') || 1,
                    cRow, cRows, cIndex,
                    pRows, pRow, pCell, pIndex,
                    i;

                if (this.tagName == 'TD') {
                    cRow = this.parentNode;
                    cRows = cRow.parentNode.children;
                    cIndex = Array.prototype.indexOf.call(cRows, cRow);

                    if (isAcceptable(this, span) === false) {
                        return false;
                    }

                    this.rowSpan = span;
                    for (i = 1; i < span; i++) {
                        cRows[cIndex + i].firstElementChild.style.display = 'none';
                    }
                }

                if (dragging.parentNode.tagName == 'TD') {
                    pCell = dragging.parentNode;
                    pRow = pCell.parentNode;
                    pRows = pRow.parentNode.children;
                    pIndex = Array.prototype.indexOf.call(pRows, pRow);

                    for (i = 1; i < +pCell.rowSpan; i++) {
                        pRows[pIndex + i].firstElementChild.style.display = '';
                    }
                    dragging.parentNode.removeAttribute('rowSpan');
                }

                this.appendChild(dragging);
            }

            return true;
        }
    };

    function isAcceptable(cell, span) {
        var row = cell.parentNode,
            rows = row.parentNode.children,
            index = Array.prototype.indexOf.call(rows, row),
            i;

        for (i = 0; i < span; i++) {
            if (!rows[index + i] || rows[index + i].firstElementChild.innerHTML !== '') {
                return false;
            }
        }

        return true;
    }

    function draggable(element) {
        element.addEventListener('dragstart', listeners['dragstart'], false);
        element.addEventListener('dragend', listeners['dragend'], false);
    }

    function droppable(element) {
        element.addEventListener('dragenter', listeners['dragenter'], false);
        element.addEventListener('dragover', listeners['dragover'], false);
        element.addEventListener('dragleave', listeners['dragleave'], false);
        element.addEventListener('drop', listeners['drop'], false);
    }

    foreach.call(nodes, draggable);
    foreach.call(cells, droppable);
    droppable(source);
</script>