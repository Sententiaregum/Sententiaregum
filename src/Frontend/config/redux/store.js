/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian@mbosch.me>
 * (c) Ben Bieler <ben@benbieler.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { combineReducers, createStore, applyMiddleware }  from 'redux';
import { routerReducer }                                  from 'react-router-redux';
import reducers                                           from './reducers';
import thunk                                              from 'redux-thunk';

/**
 * Higher Order reducer
 *
 * @type {Reducer<S>}
 */
const rootReducer = combineReducers({ ...reducers, routing: routerReducer });

// initial state is not needed here since the `@@redux/INIT` action runs through all reducers
// which return the initial state if they can't handle the given store action.
export default createStore(rootReducer, applyMiddleware(thunk));
