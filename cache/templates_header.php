<?php class_exists('Template') or exit; ?>
<?php foreach($styles as $style): ?>
    <link href="<?php echo $style ?>" rel="stylesheet" />
<?php endforeach; ?>
<script type="text/javascript">
    function getObj(id, arr, key) { key = key || 'id'; var o = null; $.each(groups, function (i, el) { if (el[key] == id) { o=el; return; } }); return o; };
    function cleanObj(o) { Object.keys(o).forEach(key => o[key] === undefined ? delete o[key] : {}); return o; }

    Array.prototype.sum = function (prop) {
        var total = 0
        for ( var i = 0, _len = this.length; i < _len; i++ ) {
            total += parseInt(this[i][prop]);
        }
        return total;
    }
    function formatNumber(num) {
        if (isNaN(num)) {
            return '';
        }
        return num.toString().replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1 ')
    }
</script>