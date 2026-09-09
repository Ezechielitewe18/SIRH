<?php $pageTitle = 'Conversation'; ?>
<?php ob_start(); ?>

<style>
.chat-box .msg{border-radius:12px;padding:9px 13px;max-width:75%;margin-bottom:8px;word-wrap:break-word}
.chat-box .msg.moi{background:#007bff;color:#fff;margin-left:auto;border-bottom-right-radius:2px}
.chat-box .msg.lui{background:#f1f3f5;color:#333;border-bottom-left-radius:2px}
.chat-box .msg .heure{font-size:10.5px;opacity:.8;display:block;margin-top:3px}
.chat-box{max-height:480px;overflow-y:auto;display:flex;flex-direction:column}
.chat-box::-webkit-scrollbar{width:6px}
.chat-box::-webkit-scrollbar-thumb{background:#ccc;border-radius:3px}
</style>

<section class="content-header">
    <h1><i class="fas fa-user-circle text-primary"></i> <?= htmlspecialchars($nomInterlocuteur) ?></h1>
    <a href="<?= APP_URL ?>/messages" class="btn btn-secondary btn-sm" style="float:right;margin-top:-28px;">
        <i class="fas fa-arrow-left"></i> Retour
    </a>
</section>

<section class="content">
    <div class="row justify-content-center">
        <div class="col-md-8">
            <div class="card">
                <div class="card-body">
                    <div class="chat-box" id="chatBox">
                        <?php if (empty($messages)): ?>
                        <div class="text-center text-muted py-3">Aucun message. Écrivez le premier !</div>
                        <?php endif; ?>
                        <?php foreach ($messages as $m):
                            $moi = (int)$m['id_expediteur'] === (int)$_SESSION['user_id']; ?>
                        <div class="msg <?= $moi ? 'moi' : 'lui' ?>">
                            <?= nl2br(htmlspecialchars($m['contenu'])) ?>
                            <span class="heure"><?= date('d/m H:i', strtotime($m['created_at'])) ?></span>
                        </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <form method="post" action="<?= APP_URL ?>/messages/conversation/<?= $idConversation ?>" id="chatForm">
                    <?= csrf_field() ?>
                    <div class="card-footer">
                        <div class="input-group">
                            <input type="text" name="contenu" class="form-control" placeholder="Écrire un message..." autofocus required>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-primary"><i class="fas fa-paper-plane"></i> Envoyer</button>
                            </div>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>

<?php $extraScripts = '
<script>
var CHAT_URL = "' . APP_URL . '/messages/conversation/' . $idConversation . '";
var lastScroll = document.getElementById("chatBox").scrollHeight;
var chatBox = document.getElementById("chatBox");
chatBox.scrollTop = chatBox.scrollHeight;

function pollMessages(){
  fetch(CHAT_URL, { headers: { "X-Requested-With": "fetch" } })
    .then(function(r){ return r.text(); })
    .then(function(html){
      var parser = new DOMParser();
      var doc = parser.parseFromString(html, "text/html");
      var fresh = doc.getElementById("chatBox");
      if (!fresh) return;
      var newBox = fresh.innerHTML;
      if (newBox !== chatBox.innerHTML){
        var stick = Math.abs(chatBox.scrollHeight - chatBox.scrollTop - chatBox.clientHeight) < 40;
        chatBox.innerHTML = newBox;
        if (stick) chatBox.scrollTop = chatBox.scrollHeight;
      }
    }).catch(function(){});
}
setInterval(pollMessages, 5000);

document.getElementById("chatForm").addEventListener("submit", function(){
  setTimeout(function(){ chatBox.scrollTop = chatBox.scrollHeight; }, 100);
});
</script>
'; ?>
<?php
$content = ob_get_clean();
require __DIR__ . '/../layouts/app.php';
?>