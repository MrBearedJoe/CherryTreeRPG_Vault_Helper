</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.2.3/dist/js/bootstrap.bundle.min.js"
  integrity="sha384-kenU1KFdBIe4zVF0s0G1M5b4hcpxyD9F7jL+jjXkk+Q2h455rYXK/7HAuoJl+0I4" crossorigin="anonymous"></script>
<script>
setTimeout(() => {


  const successCodeTr = document.querySelector(`td[data-success-code]`)
  if (successCodeTr) {
    const successCodeDiv = document.querySelector(`div#successCode`)
    console.log(successCodeTr)
    console.log(successCodeDiv)

    const theSuccessText = successCodeTr.textContent
    const alert = `
  <div class="alert alert-success m-4 p-2" role="alert">
  <h4 class="alert-heading">Cracked! The code is: <b>${theSuccessText}</b></h4>
  </div>
  `
    successCodeDiv.innerHTML = alert

  }
}, 0)


const copyBtn = document.querySelector(`button[data-copy-code-btn]`)
copyBtn.addEventListener("click", () => {
  const copyBtn = document.querySelector(`span[data-copy-codes]`)
  console.log(copyBtn)
  let copyText = copyBtn.innerHTML
  console.log(copyText)
  copyText = copyText.split(" ")
  let copyT = ''
  for (let i in copyText) {
    console.log(`check${i}`, copyText[i].length, copyText[i])

    if (copyText[i].length >= 6) copyT += `${copyText[i]}\r\n`
  }
  console.log(copyT)
  copyToClipboard(copyT)

})

function copyToClipboard(text) {
  var copyText = document.queryCommandSupported('copy');
  var copyTextArea = document.createElement("textarea");
  copyTextArea.value = text;
  document.body.appendChild(copyTextArea);
  copyTextArea.select();
  document.execCommand('copy');
  document.body.removeChild(copyTextArea);
}
</script>
</body>

</html>