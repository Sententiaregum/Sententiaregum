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
import Login from '../components/portal/Login';
import CreateAccount from '../components/portal/CreateAccount';
import ActivateAccount from '../components/portal/ActivateAccount';
import NotFoundPage from '../components/app/layout/NotFoundPage';
import Application from '../components/app/layout/Application';
import { IndexRoute, Router, Route, hashHistory } from 'react-router';

export default (
  <Router history={hashHistory}>
    <Route component={Application} path="/">
      <IndexRoute component={Login} />
      <Route component={CreateAccount} path="sign-up" />
      <Route component={ActivateAccount} path="activate/:name/:key" />
      <Route component={NotFoundPage} path="*" />
    </Route>
  </Router>
);
