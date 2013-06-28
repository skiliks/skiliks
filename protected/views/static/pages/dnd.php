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

    .column table {
        width: 100%;
        border-collapse: collapse;
    }
    .column table td {
        border: 1px dotted #aaa;
        height: 20px;
    }
    .column table td.over {
        border: 1px solid #444;
    }

    .node {
        border: 1px solid #333;
        background: #f0f0f0;
        padding: 5px;
        margin-bottom: 4px;
        font: 18px/150% tahoma, sans-serif;
        overflow: hidden;
    }

    .hidden {
        background: red;
    }
</style>

<div class="columns">
    <div class="source column">
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
        <div class="node" draggable="true">Some long piece of text...</div>
    </div>
    <div class="target column">
        <table>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
        </table>
    </div>
    <div class="target column">
        <table>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
        </table>
    </div>
    <div class="target column">
        <table>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
            <tr><td>&nbsp;</td></tr>
        </table>
    </div>
</div>

<script type="text/javascript">
    var nodes = document.querySelectorAll('.source .node'),
        cells = document.querySelectorAll('.target td'),
        foreach = Array.prototype.forEach,
        listeners;

    listeners = {
        dragstart: function(e) {
            console.log(e.type);

            this.classList.add('hidden');
            e.dataTransfer.effectAllowed = 'move';
            e.dataTransfer.setData('text/html', this.outerHTML);
        },
        dragend: function(e) {
            console.log(e.type);

            var hidden = document.querySelector('.node.hidden');
            if (hidden) {
                hidden.classList.remove('hidden');
            }
        },
        dragenter: function(e) {
            console.log(e.type);
            console.log(this);
            this.classList.add('over');
            e.dataTransfer.dropEffect = 'move';
        },
        dragover: function(e) {
            //console.log(e.target);
        },
        dragleave: function(e) {
            console.log(e.type);
            console.log(this);
            this.classList.remove('over');
        },
        drop: function(e) {

        }
    };

    foreach.call(nodes, function(node) {
        node.addEventListener('dragstart', listeners['dragstart'], false);
        node.addEventListener('dragend', listeners['dragend'], false);
        node.addEventListener('drop', listeners['drop'], false);
    });

    foreach.call(cells, function(cell) {
        cell.addEventListener('dragenter', listeners['dragenter'], false);
        cell.addEventListener('dragleave', listeners['dragleave'], false);
    });
</script>