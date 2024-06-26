import CryptoJS from 'crypto-js';

const secret = "W€b8t?I/_.Mxct31O78.ô"

export function setLocalStorage(storageItem, value) {
  if(value == null) localStorage.setItem(storageItem, null);
  else {
    const crypted = CryptoJS.AES.encrypt(value.toString(), secret).toString();
    localStorage.setItem(storageItem, crypted);
  }
}

export function getLocalStorage(storageItem, isBoolean = false) {
  const value = localStorage.getItem(storageItem);
  if(value != null) {
    let decryptedString = null;
    try {
      const decryptedBytes = CryptoJS.AES.decrypt(value, secret);
      decryptedString = decryptedBytes.toString(CryptoJS.enc.Utf8);
    } catch(e) {
      console.log("Error decrypting value from localStorage: " + e);
      return null;
    }
    return isBoolean ? (decryptedString === "true" ? true : false) : decryptedString;
  } else {
    return null;
  }
}

export function removeLocalStorage(storageItem) {
  localStorage.removeItem(storageItem);
}

export async function auth_fetch(endpoint, method = "GET", body = null) {
  const bodyContent = body ? {body: JSON.stringify(body)} : null;

  const res = await fetch(`http://localhost:5151${endpoint}`, {
      method: method,
      headers: {
        'AUTHORIZATION': 'Bearer ' + getLocalStorage('accessToken')
      },
      ...bodyContent
  });

  if (res.status === 401) {
    const res = await fetch(`http://localhost:5151/refresh`, {
      method: "GET",
      headers: {
        'AUTHORIZATION': 'Bearer ' + getLocalStorage('refreshToken')
      }
    });

    if (res.status === 401) {
      removeLocalStorage('accessToken');
      removeLocalStorage('refreshToken');
      setLocalStorage('toast','Relácia vypršala. Prihláste sa znovu.');
      window.location.href = "/login";
      return null;
    } else {
      const data = await res.json();
      setLocalStorage('accessToken', data.accessToken);
      return await fetch(`http://localhost:5151${endpoint}`, {
          method: method,
          headers: {
            'AUTHORIZATION': 'Bearer ' + getLocalStorage('accessToken')
          },
          ...bodyContent
      });
    }
  } else {
    return res;
  }
}