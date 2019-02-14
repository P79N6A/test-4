import { NgModule }       from '@angular/core';
import { BrowserModule }  from '@angular/platform-browser';

import { AppComponent } from './app.component';

import { jqxCheckBoxComponent } from 'components/angular_jqxcheckbox';

@NgModule({
    imports: [BrowserModule],
    declarations: [AppComponent, jqxCheckBoxComponent],
    bootstrap: [AppComponent]
})
export class AppModule { }

