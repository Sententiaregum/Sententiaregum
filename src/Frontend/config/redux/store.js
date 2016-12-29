/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import {combineReducers, createStore}   from 'redux';
import {routerReducer}                  from 'react-router-redux';
import reducers                         from './reducers';
import initialState                     from './initialState';

/**
 * Higher Order reducer
 *
 * @type {Reducer<S>}
 */
const rootReducer = combineReducers({...reducers, routing: routerReducer});

export default createStore(rootReducer, initialState);
