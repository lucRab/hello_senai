
const botao  = document.querySelector('.botao');


botao.addEventListener("click", function(e) {
  e.preventDefault();
  const titulo = document.querySelector('.titulo');
  const descricao = document.querySelector('.descricao');
  const imagem = document.querySelector('.imagem');
  const formData = new FormData();
  formData.append('nomeProjeto', titulo.value);
  formData.append('descricao', descricao.value);
  formData.append('image', imagem.files[0]);
  formData.append('status', "Em procedimento");
  formData.append('link', "https://github.com/lucRab/hello_senai/blob/pach-branch");
  console.log(imagem.files[0]);
  const fetchTeste = fetch('http://127.0.0.1:8000/api/v1/projeto', {
    method: 'POST',
    headers: {
      
      Authorization: 'Bearer ' + '18|e7B8SBwvq1H6KgLSoSnYTX5U9fJEfYRbC9SqAIO9f3ddb0e5',
    },
    body: formData
  });
})
