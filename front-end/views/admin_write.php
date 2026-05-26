<?php
$isEdit   = !empty($editId);
$formAction = $isEdit ? $base . '/admin/edit' : $base . '/admin/write';
$formTitle  = (string)($form['title']   ?? '');
$formExcerpt = (string)($form['excerpt'] ?? '');
$formContent = (string)($form['content'] ?? '');
?>

<div class="write-page">
    <div class="write-toolbar">
        <span><?= $isEdit ? 'Editar ensaio' : 'Novo ensaio' ?></span>
        <div class="write-actions">
            <a class="btn-outline" href="<?= $base ?>/admin">← Painel</a>
            <button type="submit" form="write-form" class="btn-publish">
                <?= $isEdit ? 'Guardar' : 'Guardar rascunho' ?>
            </button>
        </div>
    </div>

    <form id="write-form" method="post" action="<?= $base ?>/admin/write">
        <input type="hidden" name="_csrf" value="<?= e($csrf) ?>">
        <?php if ($isEdit): ?>
            <input type="hidden" name="id" value="<?= (int)$editId ?>">
        <?php endif; ?>

        <textarea
            class="write-title-input"
            name="title"
            placeholder="Título"
            rows="1"
            required
            oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
        ><?= e($formTitle) ?></textarea>

        <textarea
            class="write-excerpt-input"
            name="excerpt"
            placeholder="Resumo breve (mínimo 10 caracteres)…"
            rows="2"
            required
            oninput="this.style.height='auto';this.style.height=this.scrollHeight+'px'"
        ><?= e($formExcerpt) ?></textarea>

        <textarea
            class="write-body-input"
            name="content"
            placeholder="Começa a escrever…"
            required
        ><?= e($formContent) ?></textarea>
    </form>
</div>
