  </div><!-- /p-8 -->
</main>

<?php if (!empty($toast_msg)): ?>
<div class="toast bg-[#122135] border border-green-500/30 text-green-300" id="toast">
  <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M10 18a8 8 0 100-16 8 8 0 000 16zm3.707-9.293a1 1 0 00-1.414-1.414L9 10.586 7.707 9.293a1 1 0 00-1.414 1.414l2 2a1 1 0 001.414 0l4-4z" clip-rule="evenodd"/></svg>
  <?= htmlspecialchars($toast_msg) ?>
</div>
<script>setTimeout(() => { const t = document.getElementById('toast'); if(t) t.remove(); }, 3500);</script>
<?php endif; ?>

<?php if (!empty($toast_error)): ?>
<div class="toast bg-[#122135] border border-red-500/30 text-red-300" id="toast-err">
  <svg class="w-4 h-4 shrink-0" fill="currentColor" viewBox="0 0 20 20"><path fill-rule="evenodd" d="M18 10a8 8 0 11-16 0 8 8 0 0116 0zm-7 4a1 1 0 11-2 0 1 1 0 012 0zm-1-9a1 1 0 00-1 1v4a1 1 0 102 0V6a1 1 0 00-1-1z" clip-rule="evenodd"/></svg>
  <?= htmlspecialchars($toast_error) ?>
</div>
<script>setTimeout(() => { const t = document.getElementById('toast-err'); if(t) t.remove(); }, 4000);</script>
<?php endif; ?>

</body>
</html>
