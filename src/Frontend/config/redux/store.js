/*
 * This file is part of the Sententiaregum project.
 *
 * (c) Maximilian Bosch <maximilian.bosch.27@gmail.com>
 * (c) Ben Bieler <benjaminbieler2014@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

import { combineReducers, createStore, applyMiddleware, compose }  from 'redux';
import { routerReducer }                                       from 'react-router-redux';
import reducers                                           from './reducers';
import thunk                                              from 'redux-thunk';
import userReducer                                        from '../../reducers/user/userReducer';
import persistState from 'redux-localstorage'

/**
 * Higher Order reducer
 *
 * @type {Reducer<S>}
 */
const rootReducer = combineReducers({ ...reducers, routing: routerReducer });
const enhancer = compose (
  applyMiddleware(thunk),
  persistState('form'),
  window.__REDUX_DEVTOOLS_EXTENSION__ && window.__REDUX_DEVTOOLS_EXTENSION__()
);

// initial state is not needed here since the `@@redux/INIT` action runs through all reducers
// which return the initial state if they can't handle the given store action.
/* eslint-disable no-underscore-dangle */
export default createStore(rootReducer, enhancer);
/* eslint-enable */

