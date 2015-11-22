<div id="content_title">
    <h1>
        <?= $titleForLayout ?>
    </h1>
</div>

<div class="row">
    <div class="col-sm-offset-2 col-sm-8">
        <table id="stats" class="table">
            <tbody>
                <?php foreach ($stats as $label => $value): ?>
                    <tr>
                        <th>
                            <?= $label ?>
                        </th>
                        <td>
                            <?= $value ?>
                        <td>
                    </tr>
                <?php endforeach; ?>
            </tbody>
        </table>
    </div>
</div>