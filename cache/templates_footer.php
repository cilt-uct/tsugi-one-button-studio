<?php class_exists('Template') or exit; ?>
<?php foreach($scripts as $script): ?>
    <script src="<?php echo $script ?>" type="text/javascript"></script>
<?php endforeach; ?>
   
<script type="text/javascript">
    jQuery.fn.exists = function(){ return this.length > 0; }
    
    $(function() {
        
        
        $("#users").submit(function( event ) {
            event.preventDefault();

            let list = [],
                out = $('#result'),
                btn = $('button[type="submit"].btn-success'),
                now = new Date().toLocaleString();
                success = `<div class="alert alert-success" role="alert">Saved list (${now})</div>`;
            $('#users input[type=checkbox]:checked').each(function(i, el){ list.push(JSON.parse($(el).val())); });

            btn.addClass('disabled').attr('disabled', true).html('<i class="fas fa-spinner fa-pulse"></i>&nbsp;&nbsp;Updating...');

            $.post( $("#users").attr('action'), { list: list }, function(data) {
                if(data['err'] === 0) {
                    out.html(success);
                } else {
                    out.html(`<div class="alert alert-danger" role="alert">Saving list failed - ${data['msg']}</div>`)
                }
            })
            .fail(function(jqXHR, textStatus, error) {
                out.html(`<div class="alert alert-danger" role="alert">Failed to submit list - ${error}</div>`);
            })
            .always(function() {
                btn.removeClass('disabled').attr('disabled', false).html(btn.data('title'));
            });
        });
    });
</script>
