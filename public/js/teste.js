
const botao  = document.querySelector('.botao');
localStorage.setItem('token', '10|2oAeqPaTAcjtBQDGs8Rr1sTq3jKws8a6WXzGLEvlcec8e30b');
//
botao.addEventListener("click", function(e) {
  e.preventDefault();
  const titulo = document.querySelector('.titulo');
  const descricao = document.querySelector('.descricao');
  const imagem = document.querySelector('.imagem');
  const formData = new FormData();
  formData.append('titulo', titulo.value);
  formData.append('nomeProjeto', titulo.value);
  formData.append('descricao', descricao.value);
  formData.append('imagem', imagem.files[0]);
  formData.append('status', "Em procedimento");
  formData.append('link', "https://github.com/lucRab/hello_senai/blob/pach-branch");
  console.log(imagem.files[0]);
  const fetchTeste = fetch('http://127.0.0.1:8000/api/v1/desafio', {
    method: 'POST',
    headers: {
      
      Authorization: 'Bearer ' + '11|PGSjI7wA562OD76WxHkVzvR6SN1SWfkjfIbkKecDf88437f0',
    },
    body: formData
  });
})
