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

/**
 * Component which combines a button bar with a component.
 *
 * @author Maximilian Bosch <maximilian.bosch.27@gmail.com>
 */
export default class LoadableButtonBar extends React.Component {
  /**
   * Constructor.
   *
   * @param {Array} props Component properties.
   *
   * @returns {void}
   */
  constructor(props) {
    super(props);

    this.state = {
      progress: this.props.progress
    };
  }

  /**
   * Lifecycle hook to modify the progress flag.
   *
   * @param {Object} nextProps Property changeset.
   *
   * @returns {void}
   */
  componentWillReceiveProps(nextProps) {
    this.setState({ progress: nextProps.progress ? nextProps.progress : false });
  }

  /**
   * Renders the component.
   *
   * @returns {React.Element} The vDOM markup.
   */
  render() {
    let spinner;
    const props = {};
    if (this.state.progress) {
      spinner        = this._renderSpinner();
      props.disabled = 'disabled';
    }

    return (
      <div className="form-group">
        <button type="submit" className="btn btn-primary spinner-btn" {...props}>{this.props.btnLabel}</button>
        {spinner}
      </div>
    );
  }

  /**
   * Renders the loading spinner.
   *
   * @returns {React.Element} Markup of the loading spinner.
   * @private
   */
  _renderSpinner() {
    return (
      <div className="sk-double-bounce custom-spinner">
        <div className="sk-child sk-double-bounce1"></div>
        <div className="sk-child sk-double-bounce2"></div>
      </div>
    );
  }
}

LoadableButtonBar.propTypes = {
  btnLabel: React.PropTypes.string,
  progress: React.PropTypes.bool
};
