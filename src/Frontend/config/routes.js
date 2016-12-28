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

import React                                         from 'react';
// import Login                                      from '../components/portal/Login';
// import CreateAccount                              from '../components/portal/CreateAccount';
// import ActivateAccount                            from '../components/portal/ActivateAccount';
// import NotFoundPage                               from '../components/app/layout/NotFoundPage';
import Application                                   from '../components/app/layout/Application';
// import DashboardIndex                             from '../components/network/dashboard/Index';
// import Logout                                     from '../components/portal/Logout';
import {IndexRoute, Router, Route, hashHistory}      from 'react-router';
import {redirectToDashboard, protectPage} from '../util/react/routerHooks';
import {syncHistoryWithStore}                        from 'react-router-redux';
import {Provider}                                    from 'react-redux';
import store                                         from '../config/store';

const HelloWorld = () => {
  return (
    <h1>Hello World!</h1>
  )
};

//TODO: Change to browesrHistory
const history = syncHistoryWithStore(hashHistory, store);

export default (
  <Provider store={store}>
    <Router history={history}>
      <Route component={Application} path="/">
        <IndexRoute component={HelloWorld}/>
        <Route component={HelloWorld} path="sign-up"/>
        <Route component={HelloWorld} path="activate/:name/:key"/>

        <Route component={HelloWorld} path="/logout"/>
        <Route component={HelloWorld} path="dashboard" onEnter={protectPage}/>

        <Route component={HelloWorld} path="*"/>
      </Route>
    </Router>
  </Provider>
);