/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

'use strict';

import React from 'react';
import HelloWorld from '../components/HelloWorld';
import CreateAccount from '../components/portal/CreateAccount';
import ActivateAccount from '../components/portal/ActivateAccount';
import NotFoundPage from '../components/app/NotFoundPage';
import { Router, Route, hashHistory } from 'react-router';
import ReactPageComponentDecorator from '../components/app/ReactPageComponentDecorator';
import { portal, network } from './menu';
import ComponentBuilder from '../util/react/ComponentBuilder';

const HelloWorldPage      = ComponentBuilder.buildGenericComponentForPage(HelloWorld, portal, {});
const CreateAccountPage   = ComponentBuilder.buildGenericComponentForPage(CreateAccount, portal, {});
const ActivateAccountPage = ComponentBuilder.buildGenericComponentForPage(ActivateAccount, portal, {});

export default (
  <Router history={hashHistory}>
    <Route component={HelloWorldPage} path="/" />
    <Route component={CreateAccountPage} path="/sign-up" />
    <Route component={ActivateAccountPage} path="/activate/:name/:key" />
    <Route component={NotFoundPage} path="*" />
  </Router>
);
