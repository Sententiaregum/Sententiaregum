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

  static propTypes = {
    actions:          PropTypes.object.isRequired,
    name_suggestions: PropTypes.array,
    success:          PropTypes.bool.isRequired
  };

  /**
   * Handle the onSubmit event.
   *
   * @param {Object} data The input data.
   *
   * @returns {Promise} The promise for the account creation.
   */
  handleSubmit(data) {
    return this.props.actions.sign_up.createAccount(data);
  }

  render() {
    return (
      <div>
        <h1><Translate content="pages.portal.head" /></h1>
        <div>
          <InfoBox />
          <Form onSubmit={(...args) => this.handleSubmit(...args)} name_suggestions={this.props.name_suggestions} success={this.props.success} />
        </div>
      </div>
    );
  }
}

const mapStateToProps = state => ({
  name_suggestions: state.user.registration.name_suggestions,
  success:          state.user.registration.success
});

const mapDispatchToProps = dispatch => ({
  actions: {
    sign_up: bindActionCreators(userActions, dispatch)
  }
});

export default connect(
  mapStateToProps,
  mapDispatchToProps
)(CreateAccount);

