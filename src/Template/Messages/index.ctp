<?php
    use Cake\Routing\Router;
?>
<div id="content_title">
    <h1>
        <?php echo $title_for_layout; ?>
    </h1>
</div>

<div id="conversations">
    <?php if (empty($conversations)): ?>
        <div class="row">
            <div class="col-sm-offset-2 col-sm-8">
                <p class="no_messages">
                    No messages sent or received. :(
                </p>
            </div>
        </div>
    <?php else: ?>
        <div class="row">
            <div class="col-sm-offset-2 col-sm-8">
                <h2>
                    Select a Conversation
                </h2>
            </div>
        </div>
        <div id="conversations_index">
            <?php foreach ($conversations as $other_user_id => $conversation): ?>
                <div class="row">
                    <div class="col-sm-offset-2 col-sm-8">
                        <a href="<?= Router::url(['action' => 'conversation', $conversation['color']]) ?>" data-color="<?= $conversation['color'] ?>" class="row">
                            <span class="col-sm-2">
                                <span class="penpal">
                                    <span class="colorbox" style="background-color: #<?= $conversation['color'] ?>"></span>
                                    <span class="colorhex">
                                        #<?= $conversation['color'] ?>
                                    </span>
                                </span>
                            </span>
                            <span class="col-sm-10">
                                <span class="excerpt_meta">
                                    <?= ucwords($conversation['verb']) ?>
                                    <?= $this->Time->abbreviatedTimeAgoInWords($conversation['time']) ?>:
                                </span>
                                <span class="excerpt">
                                    <?= $conversation['message'] ?>
                                </span>
                            </span>
                        </a>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php endif; ?>
</div>