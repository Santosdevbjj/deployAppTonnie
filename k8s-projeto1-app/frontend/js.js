document.getElementById('formMensagem').addEventListener('submit', async (e) => {
  e.preventDefault();
  const data = Object.fromEntries(new FormData(e.target).entries());
  const resp = await fetch('/api/mensagem', {
    method: 'POST',
    headers: {'Content-Type':'application/json'},
    body: JSON.stringify(data)
  });
  const text = await resp.text();
  document.getElementById('saida').textContent = `Status: ${resp.status}\n${text}`;
});
