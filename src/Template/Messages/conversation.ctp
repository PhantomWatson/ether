<div id="content_title">
    <h1>
        <?= $titleForLayout ?>
    </h1>
</div>
<p>
    <?= $this->Html->link(
        '<span class="glyphicon glyphicon-arrow-left" aria-hidden="true"></span> Back to conversations',
        ['action' => 'index'],
        ['escape' => false]
    ) ?>
</p>

<div id="conversation">
    <?php if (empty($messages)): ?>
        <p>
            You have not exchanged any messages with this Thinker yet.
        </p>
    <?php else: ?>
        <?php
            $pagingUsed = $this->Paginator->hasNext() || ($this->Paginator->hasPrev() && ! $this->request->is('ajax'));
            $this->Paginator->templates([
                'nextActive' => '<a href="{{url}}">{{text}}</a>',
                'prevActive' => '<a href="{{url}}">{{text}}</a>'
            ]);
            if ($pagingUsed) {
                echo '<p class="paging">';
                $current = $this->Paginator->counter('{{current}}');
                $count = $this->Paginator->counter('{{count}}');
                echo 'Showing <span id="totalMsgShown">'.number_format($current).'</span> ';
                echo 'of '.number_format($count).' messages. ';
                if ($this->Paginator->hasNext()) {
                    echo $this->Paginator->next('Show older messages');
                }
                if ($this->Paginator->hasPrev() && ! $this->request->is('ajax')) {
                    $label = 'show newer messages';
                    $label = $this->Paginator->hasNext() ? ", $label," : ucfirst($label);
                    echo $this->Paginator->prev($label);
                }
                echo ' or <a href="?full">show full conversation</a>.';
                echo '</p>';
            }
        ?>
        <?php foreach ($messages as $message): ?>
            <?= $this->element('Messages/message', [
                'message' => $message
            ]) ?>
        <?php endforeach; ?>
    <?php endif; ?>
</div>

<div id="send_message" class="row">
    <?php if ($penpalAcceptsMessages): ?>
        <div class="col-sm-offset-3 col-sm-6">
            <?php
                echo $this->Form->create(
                    $messageEntity,
                    [
                        'url' => ['controller' => 'Messages', 'action' => 'send']
                    ]
                );
                echo $this->Form->input(
                    'message',
                    [
                        'class' => 'form-control',
                        'div' => ['class' => 'form-group'],
                        'label' => false,
                        'placeholder' => 'Send a message'
                    ]
                );
                echo $this->Form->input(
                    'recipient',
                    [
                        'type' => 'hidden',
                        'value' => $penpalColor
                    ]
                );
                echo $this->Form->submit(
                    'Send',
                    ['class' => 'btn btn-default']
                );
                echo $this->Form->end();
            ?>
        </div>
    <?php else: ?>
        <div class="col-sm-offset-2 col-sm-8">
            This user has opted out of receiving messages.
        </div>
    <?php endif; ?>
</div>