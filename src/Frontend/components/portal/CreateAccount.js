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

import React, { Component, PropTypes }     from 'react';
import Translate                           from 'react-translate-component';
import { connect            }              from 'react-redux';
import { bindActionCreators }              from 'redux';
import Form                                from './signup/Form';
import InfoBox                             from './signup/InfoBox';
import * as userActions                    from '../../actions/userActions';

/**
 * Presentational component for the sign-up page
 *
 * @author Benjamin Bieler <ben@benbieler.com>
 */
class CreateAccount extends Component {

  static PropTypes = {
    actions: PropTypes.object.isRequired
  };

  static contextTypes = {
    store: PropTypes.object
  };

  /**
   * Handle the onSubmit event
   *
   * @param data
   * @param recaptchaHash
   */
  handleSubmit = (data) => {
    this.props.actions.sign_up.createAccount(data);
  };

  render() {

    const { store }      = this.context;
    let name_suggestions = [], success = false;

    store.subscribe(() => {
      name_suggestions = store.getState().user.registration.name_suggestions;
      success          = store.getState().user.registration.success;
    });

    return (
      <div>
        <h1><Translate content="pages.portal.head" /></h1>
        <div>
          <InfoBox />
          <Form onSubmit={this.handleSubmit} name_suggestions={name_suggestions} success={success} />
        </div>
      </div>
    );
  }
}

const mapStateToProps = state => ({});

const mapDispatchToProps = dispatch => ({
  actions: {
    sign_up: bindActionCreators(userActions, dispatch)
  }
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(CreateAccount);

