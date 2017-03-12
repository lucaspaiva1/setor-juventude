import { NativeStorage } from 'ionic-native';
import { Injectable } from '@angular/core';
import { Events } from 'ionic-angular';
import { Headers, Http, Response } from '@angular/http';
import { User } from '../model/User';
import 'rxjs/add/operator/toPromise';


@Injectable()
export class UserService {

  private headers = new Headers({ 'Content-Type': 'application/json' });

  constructor(public http: Http, public events: Events) {
  }

  public atualizarUsuario(id: number): Promise<any> {
    return this.http.post('http://dsoutlet.com.br/igrejaApi/getUsuario.php', JSON.stringify(id), { headers: this.headers })
      .toPromise()
      .then(res => this.extractGetData(res))
      .catch(this.handleErrorMessage);
  }

  private extractGetData(res: Response) {
    let retorno = { error: false, user: null };
    let data = res.json();
    if (data !== false) {
      retorno.user = data;
    }
    return retorno;
  }

  private handleErrorMessage(error: any) {
    let retorno = { error: true };
    return retorno;
  }

  public set(user: User) {
    this.events.publish('user:changed', user);
    NativeStorage.setItem('usuarioAtual', user)
      .then(
      () => {
        this.events.publish('user:changed', user);
      },
      error => alert('Erro ao carregar dados')
      );
  }

  public get(): Promise<User> {
    return NativeStorage.getItem('usuarioAtual')
      .then(
      data => data,
      error => {
        return new User()
      }
      );
  }

  public deslogar() {
    return NativeStorage.remove('usuarioAtual').then(response => {
      console.log("deslogado");
    });
  }

}
