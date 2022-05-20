import { Injectable } from '@angular/core';
import { HttpClient } from '@angular/common/http';
import { environment } from '../../environments/environment.prod';
import { AuthService } from './auth.service';

@Injectable({
  providedIn: 'root'
})
export class StorageService {

  url:string=environment.BASE_URL_STORAGE;
  userToken:string;
  constructor(
    private http:HttpClient,
    private authService:AuthService
  ) { 
    this.userToken=authService.userToken;
  }
  upImages(img:File[],id){    
    const formData= new FormData();
    Array.from(img).forEach( f => formData.append('img', f));
    return this.http.post(`${this.url}/storage/${id}`,formData,{headers:{'x-token':this.userToken}});
  }
  getImages(id){
    return this.http.get(`${this.url}/storage/${id}`,{headers:{'x-token':this.userToken}});
  }
  //actualizar foto de perfil
  upPerfil(img:File,id:string){
    //constante con la imagen como formdata
    const formData = new FormData();
    //agregamos la imagen
    formData.append('img',img);
    //retornamos observable http
    return this.http.put(`${this.url}/storage/perfil/${id}`,formData,{headers:{'x-token':this.userToken}});
  }
}
